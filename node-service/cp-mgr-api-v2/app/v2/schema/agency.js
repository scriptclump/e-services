/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('agency', {
    agency_index: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      primaryKey: true
    },
    name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    type: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    address: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    contact_phone: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    contact_fax: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    contact_email: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    prim_contact: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    prim_phone: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    prim_email: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    sec_contact: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    sec_phone: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    sec_email: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    comments: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    payment_terms: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    bank_info: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    latitude: {
      type: "DOUBLE",
      allowNull: true
    },
    longitude: {
      type: "DOUBLE",
      allowNull: true
    }
  }, {
    tableName: 'agency'
  });
};
