/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_getAllBeats', {
    pjp_pincode_area_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    pjp_name: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    days: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    default_pincode: {
      type: DataTypes.STRING(8),
      allowNull: true
    },
    spoke_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    spoke_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    wh_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    rm_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    rm_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'vw_getAllBeats'
  });
};
