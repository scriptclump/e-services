/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('cities_pincodes', {
    city_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    country_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pincode: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    city: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    state: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    officename: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    officeType: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Deliverystatus: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    divisionname: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    regionname: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    circlename: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Taluk: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Telephone: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Related Suboffice: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Related Headoffice: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'cities_pincodes'
  });
};
