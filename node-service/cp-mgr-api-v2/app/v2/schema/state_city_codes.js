/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('state_city_codes', {
    scc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    state_name: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    state_code: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    city_name: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    city_code: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    dc_inc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    fc_inc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    apob_inc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '1'
    },
    is_active: {
      type: DataTypes.STRING(10),
      allowNull: true
    }
  }, {
    tableName: 'state_city_codes'
  });
};
